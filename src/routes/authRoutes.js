const express = require("express");
const rateLimit = require("express-rate-limit");
const router = express.Router();
const authController = require("../controllers/authController");

const loginLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    limit: 10,
    standardHeaders: "draft-8",
    legacyHeaders: false,
    message: {
        message: "Too many login attempts. Please try again in 15 minutes."
    },
    skipSuccessfulRequests: true
});
router.get("/login", authController.showLogin);
router.post("/login", loginLimiter, authController.login);
router.get("/logout", authController.logout);

module.exports = router;