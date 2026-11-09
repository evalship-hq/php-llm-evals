# Evals for the support bot in src/Support.php. The worker starts the app once
# and answers each test case's input.
worker php evals/worker.php

test_case "refund after the window" "Can I still return my shoes? They arrived 40 days ago."
  target src/Support.php#answer
  matches "30[- ]day"   # "30 days" and "30-day window" are both right
  judge "Says a refund isn't possible anymore, politely"

test_case "missing package" "My package never showed up and I'm getting really annoyed."
  target src/Support.php#answer
  matches "order (number|id|#)"
  judge "Asks for the order number before promising a refund or resend"

test_case "refund inside the window" "My shoes arrived last week and they don't fit. Can I send them back?"
  target src/Support.php#answer
  judge "Says they can return them for a refund"
