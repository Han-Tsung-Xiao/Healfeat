<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <header class="navbar navbar-light bg-blue shadow-sm">
        <div class="container-fluid">
            <h1>HEALFEAT</h1>
        </div>
    </header>
    <div class="vh-100" id="show">
        <div class="text-center pt-4">
            <h1 id="slogan">Building Healthier Lives Together</h1>
        </div>
        <div class="text-center">
            <button id="enter" class="btn btn-primary btn-lg" onclick="window.location.href='login.php'">Let's get start</button>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
    #show{
        background-image: url("images/index.jpg");
        background-size: cover;
    }
    header{
        background-color: #201e1f;
    }
    header h1{
        color: #feefdd;
        font-weight: bold;
        font-size: 50px;
        margin: 0px auto;
    }
    button{
        margin-top: 50px;
    }
    #slogan{
        margin-top: 50px;
        font-size: 85px;
        font-weight: bold;
        font-family: "Rye", serif;
    }
    #enter{
        font-size: 30px;
    }
</style>