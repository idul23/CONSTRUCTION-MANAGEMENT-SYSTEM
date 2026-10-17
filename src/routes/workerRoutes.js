const express = require("express");

const router = express.Router();

const workerController = require("../controllers/workerController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/workers",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    workerController.index
);

router.get(
    "/workers/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerController.showAddForm
);

router.post(
    "/workers/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerController.create
);

router.get(
    "/workers/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerController.showEditForm
);

router.post(
    "/workers/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerController.update
);

router.get(
    "/workers/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    workerController.delete
);

module.exports = router;