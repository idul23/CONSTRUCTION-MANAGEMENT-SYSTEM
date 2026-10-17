const express = require("express");

const router = express.Router();

const userController = require("../controllers/userController");

const {
    requireAuth,
    requireRole
} = require("../middleware/authMiddleware");

router.get(
    "/users",
    requireAuth,
    requireRole("Administrator"),
    userController.index
);

router.get(
    "/users/add",
    requireAuth,
    requireRole("Administrator"),
    userController.showAddForm
);

router.post(
    "/users/add",
    requireAuth,
    requireRole("Administrator"),
    userController.create
);

router.get(
    "/users/edit/:id",
    requireAuth,
    requireRole("Administrator"),
    userController.showEditForm
);

router.post(
    "/users/edit/:id",
    requireAuth,
    requireRole("Administrator"),
    userController.update
);

router.get(
    "/users/delete/:id",
    requireAuth,
    requireRole("Administrator"),
    userController.delete
);

module.exports = router;