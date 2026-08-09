# 06 Security Rules & Hardening Protocol

## 1. Authentication & Role Authorization
- Protect administrative actions with Laravel Policies (`$this->authorize()`) or role middleware (`role:admin`, `role:manager`).
- Unauthenticated guest users attempting to access protected routes must be redirected to `/login`.

---

## 2. Input Sanitization & Attack Prevention

- **CSRF Protection**: All POST/PUT/DELETE forms must contain `@csrf` directive.
- **SQL Injection Prevention**: Never concatenate raw input strings in database calls. Use parameterized queries with bindings or Eloquent query builders.
- **XSS Escaping**: Render variables in Blade views using double curly braces `{{ $var }}`. Use `{!! $var !!}` ONLY after sanitizing rich text HTML with an HTML purifier.
- **Rate Throttling**: Apply strict rate limits to sensitive routes:
  - Auth routes: `throttle:5,1` (max 5 requests per minute).
  - Checkout & Payment endpoints: `throttle:10,1`.

---

## 3. Secure File Uploads
When processing file uploads (product images, category icons, avatars):
- Enforce mime-type and size validation in Form Requests (`image|mimes:jpeg,png,webp|max:2048`).
- Generate unique random filenames to prevent path traversal attacks.
- Serve uploaded assets through symbolic storage links or secure cloud storage.
