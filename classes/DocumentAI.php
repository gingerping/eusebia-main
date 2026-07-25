<?php
/**
 * DocumentAI
 * ----------
 * Sends a student's uploaded enrollment documents (PSA birth certificate,
 * Form 137, Good Moral, Brigada Eskwela slip, 4Ps/IP certificates, etc.)
 * to Google's Gemini API so it can:
 *   1. Identify what each document actually is.
 *   2. Read the name/birthdate/LRN/school/etc. printed on it.
 *   3. Flag whether that matches what the student typed into the form.
 *
 * This is an ASSISTIVE tool for registrar staff — it never auto-approves
 * or auto-rejects an enrollment. Staff always make the final call; the AI
 * result is just a flag telling them what's worth a closer look.
 */

require_once __DIR__ . '/ai_config.php';

class DocumentAI {

    /** Document types the model is asked to choose from. */
    const DOC_TYPES = [
        'PSA Birth Certificate',
        'Form 137 / Report Card',
        'Good Moral Certificate',
        'Certificate of Completion',
        'Brigada Eskwela Commitment Slip',
        '4Ps / Pantawid Pamilya Certificate or Household ID',
        'Indigenous People (IP) Certificate',
        'Other / Unclear',
        'Unreadable',
    ];

    /** Stay comfortably under Gemini's 20MB total inline request size. */
    const MAX_INLINE_BYTES = 15 * 1024 * 1024;

    /**
     * Analyze a set of uploaded documents against the data the student
     * typed into the enrollment form.
     *
     * @param string[] $absolutePaths Absolute filesystem paths to the uploaded files.
     * @param array    $formData      Associative array of relevant form fields, e.g.
     *                                ['Full Name' => 'Dela Cruz, Juan P', 'Birthdate' => '2012-05-01', ...]
     * @return array Structured result — always includes 'success' (bool) and 'analyzed_at'.
     */
    public static function analyze(array $absolutePaths, array $formData): array {
        $analyzedAt = date('Y-m-d H:i:s');

        if (empty($absolutePaths)) {
            return [
                'success'      => false,
                'error'        => 'No documents were uploaded to analyze.',
                'analyzed_at'  => $analyzedAt,
            ];
        }

        if (!defined('GOOGLE_AI_API_KEY') || trim(GOOGLE_AI_API_KEY) === '') {
            return [
                'success'     => false,
                'error'       => 'AI reviewer is not configured yet. Add your Google AI Studio API key in classes/ai_config.php.',
                'analyzed_at' => $analyzedAt,
            ];
        }

        $parts = [];
        $skipped = [];
        $count = 0;
        $runningBytes = 0;

        foreach ($absolutePaths as $path) {
            if ($count >= AI_MAX_DOCS_PER_ANALYSIS) {
                $skipped[] = basename($path) . ' (over the per-record analysis limit)';
                continue;
            }
            if (!is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = self::mimeForExt($ext);

            if ($mime === null) {
                $skipped[] = basename($path) . ' (.' . $ext . ' files can\'t be read by the AI reviewer — please check this one manually)';
                continue;
            }

            $rawSize = filesize($path);
            $estimatedEncodedSize = (int) ceil($rawSize * 4 / 3);
            if ($runningBytes + $estimatedEncodedSize > self::MAX_INLINE_BYTES) {
                $skipped[] = basename($path) . ' (skipped to keep the request under the AI service\'s size limit — please check this one manually)';
                continue;
            }

            $data = base64_encode(file_get_contents($path));
            $parts[] = ['text' => 'Document file name: ' . basename($path)];
            $parts[] = ['inline_data' => ['mime_type' => $mime, 'data' => $data]];
            $runningBytes += $estimatedEncodedSize;
            $count++;
        }

        if (empty($parts)) {
            return [
                'success'     => false,
                'error'       => 'None of the uploaded documents could be read by the AI reviewer (unsupported file types or too large).',
                'skipped'     => $skipped,
                'analyzed_at' => $analyzedAt,
            ];
        }

        $formDataJson = json_encode($formData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $parts[] = [
            'text' => "Here is the data the student typed into the enrollment form:\n" . $formDataJson .
                      "\n\nCompare it against what you read on the documents above and respond with the JSON object only, as instructed.",
        ];

        $payload = [
            'contents'          => [['role' => 'user', 'parts' => $parts]],
            'systemInstruction' => ['parts' => [['text' => self::buildSystemPrompt()]]],
            'generationConfig'  => [
                'response_mime_type' => 'application/json',
                'temperature'        => 0.1,
            ],
        ];

        $response = self::callGeminiApi($payload);

        if (!$response['success']) {
            $response['analyzed_at'] = $analyzedAt;
            if (!empty($skipped)) $response['skipped'] = $skipped;
            return $response;
        }

        $parsed = self::extractJson($response['text']);
        if ($parsed === null) {
            return [
                'success'     => false,
                'error'       => 'The AI reviewer returned a response that could not be understood. Try re-analyzing.',
                'raw'         => $response['text'],
                'analyzed_at' => $analyzedAt,
            ];
        }

        $parsed['success']     = true;
        $parsed['analyzed_at'] = $analyzedAt;
        $parsed['model']       = defined('GOOGLE_AI_MODEL') ? GOOGLE_AI_MODEL : 'gemini-2.5-flash';
        if (!empty($skipped)) $parsed['skipped'] = $skipped;

        return $parsed;
    }

    /** Builds the instructions + required JSON schema for the model. */
    private static function buildSystemPrompt(): string {
        $types = implode(', ', self::DOC_TYPES);
        return <<<PROMPT
You are assisting registrar staff at a Philippine public high school in reviewing documents students uploaded during online enrollment. You are an assistive checker only — staff always make the final approve/reject decision, so surface useful flags rather than final verdicts.

For each document image/file you are shown, decide which of these types it most likely is: {$types}. Use "Unreadable" only if the file is genuinely too blurry/dark/corrupted to make out, and "Other / Unclear" if it's legible but doesn't match any listed type.

For each document, also read off any of these fields that are visible on it: full name, birthdate, LRN, last school attended, guardian/parent name, 4Ps household ID number, IP group/tribe. Leave a field blank if it isn't visible on that document.

Then compare what you read against the form data you're given, and list field-by-field matches (name, birthdate, LRN, last school attended, 4Ps ID, IP group — only include fields that appear on at least one document). Minor differences in formatting (e.g. "Dela Cruz, Juan" vs "Juan Dela Cruz", or "2012-05-01" vs "May 1, 2012") should count as a match. Only flag a mismatch when the underlying value is genuinely different.

Also note which of these commonly-required documents you found evidence of, and which you did not: PSA Birth Certificate, Form 137 / Report Card, Good Moral Certificate. Frame anything "not found" as "not detected in the uploaded files" rather than "missing", since staff may have already verified it another way.

Respond with ONLY a single JSON object matching exactly this structure:

{
  "documents": [
    {
      "file": "string, the file name you were given",
      "detected_type": "one of the listed types",
      "confidence": "high | medium | low",
      "extracted_fields": { "Full Name": "string or omit if not visible", "Birthdate": "...", "LRN": "...", "Last School Attended": "...", "Guardian/Parent Name": "...", "4Ps Household ID": "...", "IP Group": "..." },
      "notes": "short plain-language note, e.g. image quality issues"
    }
  ],
  "field_matches": [
    { "field": "string", "form_value": "string", "document_value": "string", "match": true, "note": "short note, empty string if nothing notable" }
  ],
  "required_docs_detected": ["subset of: PSA Birth Certificate, Form 137 / Report Card, Good Moral Certificate"],
  "required_docs_not_detected": ["subset of the same three"],
  "overall_flag": "ok | needs_review | mismatch",
  "summary": "one or two plain-language sentences for registrar staff"
}
PROMPT;
    }

    /** Maps a file extension to a Gemini-supported MIME type, or null if unsupported. */
    private static function mimeForExt(string $ext) {
        $map = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];
        // .doc/.docx and anything else: Gemini's inline vision input can't read these directly.
        return $map[$ext] ?? null;
    }

    /** Calls the Gemini generateContent API. Returns ['success'=>bool, 'text'=>string] or ['success'=>false,'error'=>string]. */
    private static function callGeminiApi(array $payload): array {
        $model = defined('GOOGLE_AI_MODEL') ? GOOGLE_AI_MODEL : 'gemini-2.5-flash';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . GOOGLE_AI_API_KEY,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_CONNECTTIMEOUT => 15,
        ]);

        $body = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return ['success' => false, 'error' => 'Could not reach the AI service: ' . $curlErr .
                '. Note: some free hosts (e.g. InfinityFree) block outgoing HTTPS requests — check with your host if this keeps happening.'];
        }

        $decoded = json_decode($body, true);

        if ($httpCode !== 200) {
            $msg = $decoded['error']['message'] ?? ('HTTP ' . $httpCode);
            if ($httpCode === 429) {
                $msg .= ' (free-tier rate limit reached — try again in a bit, or re-analyze this record later)';
            }
            return ['success' => false, 'error' => 'AI service error: ' . $msg];
        }

        $text = '';
        $candidate = $decoded['candidates'][0] ?? null;
        if ($candidate) {
            foreach ($candidate['content']['parts'] ?? [] as $block) {
                if (isset($block['text'])) {
                    $text .= $block['text'];
                }
            }
            $finishReason = $candidate['finishReason'] ?? '';
            if ($text === '' && $finishReason === 'SAFETY') {
                return ['success' => false, 'error' => 'The AI service declined to process these documents (safety filter).'];
            }
        }

        if ($text === '') {
            return ['success' => false, 'error' => 'AI service returned an empty response.'];
        }

        return ['success' => true, 'text' => $text];
    }

    /** Strips stray markdown fences (if any slipped through) and decodes JSON. */
    private static function extractJson(string $text) {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?/i', '', $text);
        $text = preg_replace('/```$/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Last resort: grab the outermost {...} in case the model added stray text.
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}