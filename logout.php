<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Logging Out</title>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="js/supabase-auth.js"></script>
</head>
<body>
    <script>
        // Automatically sign out when this page is accessed
        document.addEventListener('DOMContentLoaded', async function() {
            await SupabaseAuth.signOut();
        });
    </script>
</body>
</html>
