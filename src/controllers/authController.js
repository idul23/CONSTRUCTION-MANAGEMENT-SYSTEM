const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const { User, Role } = require("../models");

exports.showLogin = (req, res) => {
    res.render("login", { error: null });
};

exports.login = async (req, res) => {
    try {
        console.log("LOGIN CONTROLLER REACHED");

        const username = req.body.username?.trim();
        const password = req.body.password;

        if (!username || !password) {
            return res.status(400).render("login", {
                error: "Please enter your username and password."
            });
        }

        const user = await User.findOne({
            where: { username },
            include: [{ model: Role }]
        });

        if (!user) {
            return res.status(401).render("login", {
                error: "Invalid username or password."
            });
        }

        if (!user.is_active) {
            return res.status(403).render("login", {
                error: "This account is inactive."
            });
        }

        if (!user.password_hash) {
            throw new Error("Password hash is missing for this user.");
        }

        const passwordMatch = await bcrypt.compare(
            password,
            user.password_hash
        );

        if (!passwordMatch) {
            return res.status(401).render("login", {
                error: "Invalid username or password."
            });
        }

        if (!user.Role) {
            throw new Error("User role could not be loaded.");
        }

        if (!process.env.JWT_SECRET) {
            throw new Error("JWT_SECRET is missing from the .env file.");
        }

        const token = jwt.sign(
            {
                user_id: user.user_id,
                username: user.username,
                role_id: user.role_id,
                role_name: user.Role.role_name
            },
            process.env.JWT_SECRET,
            { expiresIn: "8h" }
        );

        res.cookie("token", token, {
            httpOnly: true,
            sameSite: "lax",
            secure: process.env.NODE_ENV === "production",
            maxAge: 8 * 60 * 60 * 1000
        });

        return res.redirect("/dashboard");

    } catch (error) {
        console.error("LOGIN ERROR DETAILS:", error.stack || error);

        return res.status(500).render("login", {
            error: "Login failed. Check the server terminal for LOGIN ERROR DETAILS."
        });
    }
};

exports.logout = (req, res) => {
    res.clearCookie("token");
    res.redirect("/login");
};