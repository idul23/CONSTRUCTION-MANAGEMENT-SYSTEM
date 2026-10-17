const express = require("express");

const router = express.Router();

const expenseController = require("../controllers/expenseController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/expenses",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    expenseController.index
);

router.get(
    "/expenses/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    expenseController.showAddForm
);

router.post(
    "/expenses/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    expenseController.create
);

router.get(
    "/expenses/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    expenseController.showEditForm
);

router.post(
    "/expenses/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    expenseController.update
);

router.get(
    "/expenses/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    expenseController.delete
);

module.exports = router;