<?php
/**
 * AI Document Reviewer — configuration.
 *
 * Uses Google's Gemini API, which has a genuine no-credit-card free tier
 * (good enough for a single school's enrollment volume).
 *
 * Get a key (no card needed):
 *   1. Go to https://aistudio.google.com/apikey
 *   2. Sign in with a Google account
 *   3. Click "Create API key"
 *   4. Paste it below
 *
 * Heads up: on the free tier, Google's terms allow submitted content to be
 * used to improve their models (this doesn't apply once/if you attach
 * billing). Worth knowing since these are scans of student PSA/Form 137
 * documents. See https://ai.google.dev/gemini-api/docs/pricing for terms.
 */

define('GOOGLE_AI_API_KEY', 'AQ.Ab8RN6LG_BQs4CBPR6zv-qkUy_cCgRxHebdOG5wow2bsmjK4yg');

// gemini-2.5-flash is on the free tier as of this writing (1,500
// requests/day, no card required). Check https://ai.google.dev/gemini-api/docs/models
// for the current recommended free-tier model name before deploying —
// Google renames/replaces these periodically.
define('GOOGLE_AI_MODEL', 'gemini-3.5-flash');

// Hard cap on how many documents get sent to the AI per enrollment record
// (keeps requests fast, and keeps the request under Gemini's 20MB inline
// request size limit; extra files are left unanalyzed with a note so staff
// know to check them manually).
define('AI_MAX_DOCS_PER_ANALYSIS', 6);
