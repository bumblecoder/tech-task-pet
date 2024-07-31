<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    @vite(['resources/sass/styles.scss', 'resources/js/app.js'])
</head>
<body>
<header>
    <h1>Articles List</h1>
</header>
<div id="app">
    <div class="table-container">
        <table id="articles">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Created At</th>
                <th>Image</th>
            </tr>
            </thead>
            <tbody>
            <!-- Articles will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
    <div id="pagination">
        <button id="prev">Previous</button>
        <span id="page-info">Page 1 of 10</span>
        <button id="next">Next</button>
    </div>
</div>
</body>
</html>
