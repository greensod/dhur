


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate User</title>
    <style>
    
        /* General body styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8e8e9; /* Dusky rose pink */
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Centered container for the form */
        .container {
            background-color: #ffffff; /* White card-style background */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 400px;
        }

        /* Heading styling */
        h1 {
            text-align: center;
            color: #a05268; /* Darker dusky pink */
            margin-bottom: 20px;
        }

        /* Label styling */
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }

        /* Input and textarea styling */
        input[type="number"], textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: none;
        }

        input[type="number"] {
            text-align: center;
        }

        /* Button styling */
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #a05268; /* Dusky rose pink */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #903f57; /* Darker shade for hover */
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Rate User</h1>
    <form method="POST">
        <input type="hidden" name="friend_id" value="<?php echo $friend_id; ?>">
        <label for="rating">Rating (1 to 5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>
        <label for="review">Review:</label>
        <textarea id="review" name="review" rows="4" placeholder="Write your review here..."></textarea>
        <button type="submit">Submit Rating</button>
    </form>
</div>

</body>
</html>
