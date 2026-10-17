const express = require("express");

const router = express.Router();

const supplierController = require("../controllers/supplierController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/suppliers",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    supplierController.index
);

router.get(
    "/suppliers/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    supplierController.showAddForm
);

router.post(
    "/suppliers/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    supplierController.create
);

router.get(
    "/suppliers/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    supplierController.showEditForm
);

router.post(
    "/suppliers/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    supplierController.update
);

router.get(
    "/suppliers/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    supplierController.delete
);

module.exports = router;