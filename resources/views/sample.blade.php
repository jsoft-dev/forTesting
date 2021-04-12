<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div data-controller="sample">
        <input data-sample-target="name" type="text">
        <span data-sample-target="msg"></span>
        <button data-action="click->sample#say">Click Here</button>
    </div>
</body>

</html>
