<?php

require "../auth.php";

requireRole(["Administrator"]);

require "../database.php";


$stmt = $conn->query("
    SELECT id, name, email, role
    FROM users
    ORDER BY id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Users</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }

        a {
            text-decoration: none;
        }

        .button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #333;
            color: white;
            border-radius: 4px;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>


<h1>Users</h1>


<a href="add.php" class="button">
    Add User
</a>


<a href="../index.php" class="button">
    Dashboard
</a>


<table>

    <tr>

        <th>ID</th>

        <th>Name</th>

        <th>Email</th>

        <th>Role</th>

        <th>Actions</th>

    </tr>


    <?php foreach ($users as $user): ?>

        <tr>

            <td>
                <?= $user["id"] ?>
            </td>

            <td>
                <?= htmlspecialchars($user["name"]) ?>
            </td>

            <td>
                <?= htmlspecialchars($user["email"]) ?>
            </td>

            <td>
                <?= htmlspecialchars($user["role"]) ?>
            </td>

            <td>

                <a href="edit.php?id=<?= $user["id"] ?>">
                    Edit
                </a>

                |

                <a
                    href="delete.php?id=<?= $user["id"] ?>"
                    onclick="return confirm('Are you sure you want to delete this user?')"
                >
                    Delete
                </a>

            </td>

        </tr>

    <?php endforeach; ?>


</table>


</body>

</html>