// Auto-submit the category filter (kept in a file so the Content-Security-Policy can block inline scripts)
document.querySelectorAll('select[data-autosubmit]').forEach(function (s) {
  s.addEventListener('change', function () { s.form.submit(); });
});
