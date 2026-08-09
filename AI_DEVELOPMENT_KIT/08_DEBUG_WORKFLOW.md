# 08 Debug Workflow & Incident Diagnosis

## 1. Systematic Debugging Protocol

When an error, exception, or runtime failure occurs:

```
[ Error Occurs ]
       |
       v
[ Step 1: Read Complete Stack Trace in storage/logs/laravel.log ]
       |
       v
[ Step 2: Identify Root Cause & File/Line Number ]
       |
       v
[ Step 3: Inspect Code & Database State ]
       |
       v
[ Step 4: Write Automated Test Reproducing the Issue ]
       |
       v
[ Step 5: Implement Fix in Service/Repository Layer ]
       |
       v
[ Step 6: Verify Fix with php artisan test & Log Verification ]
```

---

## 2. Inspecting Log Files
Always inspect logs before forming diagnostic hypotheses:
- Run `tail -n 100 storage/logs/laravel.log` or view latest exception blocks.
- Check Monolog context arrays to verify passed parameter values.

---

## 3. Anti-Patterns to Avoid During Debugging
- **NEVER swallow exceptions**: Do not wrap broken logic in empty `try { ... } catch (\Exception $e) {}` blocks.
- **NEVER delete failing tests**: Fix the underlying code contract until tests pass.
- **NEVER return dummy fallbacks**: Fix data providers or null pointer references at the source.
