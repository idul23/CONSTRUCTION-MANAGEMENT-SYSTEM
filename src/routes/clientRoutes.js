const express = require("express");
const router = express.Router();

const clientController = require("../controllers/clientController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/clients",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager",
        "Site Staff"
    ),
    clientController.index
);

router.get(
    "/clients/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    clientController.addForm
);

router.post(
    "/clients/add",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    clientController.create
);

router.get(
    "/clients/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    clientController.editForm
);

router.post(
    "/clients/edit/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    clientController.update
);

router.post(
    "/clients/delete/:id",
    requireAuth,
    requireRole(
        "Administrator",
        "Project Manager"
    ),
    clientController.delete
);
module.exports = router;