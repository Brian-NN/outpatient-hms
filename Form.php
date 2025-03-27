<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Form</title>
	    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
	<form class="form">
 <div class="login-container">
      
        <form method="POST" action="Form.php">
            <div class="form-group">
                <label for="username">First name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="Occupation">Occupation:</label>
                <input type="text" id="occupation" name="occupation" required>
            </div>
             <div class="form-group">
                <label for="Department">Department:</label>
                <input type="text" id="Department" name="Department" required>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>

</body>
</html>