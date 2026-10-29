const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const { User, Role } = require("../models");

exports.showLogin = (req, res) => {
    res.render("login", { error: null });
};

exports.login = async (req, res) => {
    try {
        const username = req.body.username?.trim();
        const password = req.body.password;

        if (
            typeof username !== "string" ||
            typeof password !== "string" ||
            !username ||
            !password
        ) {
            return res.status(400).render("login", {
                error: "Please enter your username and password."
            });
        }

        const user = await User.findOne({
            where: { username },
            include: [{ model: Role }]
        });

        if (!user || !user.password_hash) {
            return res.status(401).render("login", {
                error: "Invalid username or password."
            });
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

        if (!user.is_active) {
            return res.status(403).render("login", {
                error: "This account is inactive."
            });
        }

        if (!user.Role) {
            console.error("Login failed: User role could not be loaded.");

            return res.status(500).render("login", {
                error: "Something went wrong. Please try again later."
            });
        }

        if (!process.env.JWT_SECRET) {
            console.error("Login failed: JWT_SECRET is not configured.");

            return res.status(500).render("login", {
                error: "Something went wrong. Please try again later."
            });
        }

        const token = jwt.sign(
            {
                user_id: user.user_id,
                username: user.username,
                role_id: user.role_id,
                role_name: user.Role.role_name
            },
            process.env.JWT_SECRET,
            {
                expiresIn: "8h",
                algorithm: "HS256"
            }
        );

        res.cookie("token", token, {
            httpOnly: true,
            sameSite: "lax",
            secure: process.env.NODE_ENV === "production",
            maxAge: 8 * 60 * 60 * 1000,
            path: "/"
        });

        return res.redirect("/dashboard");

    } catch (error) {
        console.error("Login error:", error.message);

        return res.status(500).render("login", {
            error: "Something went wrong. Please try again later."
        });
    }
};

exports.logout = (req, res) => {
    res.clearCookie("token", {
        httpOnly: true,
        sameSite: "lax",
        secure: process.env.NODE_ENV === "production",
        path: "/"
    });

    return res.redirect("/login");
};