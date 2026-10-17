const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const { User, Role } = require("../models");

exports.showLogin = (req, res) => {
    res.render("login", {
        error: null
    });
};

exports.login = async (req, res) => {
    try {
        const { username, password } = req.body;

        const user = await User.findOne({
            where: {
                username: username
            },
            include: [
                {
                    model: Role
                }
            ]
        });

        if (!user) {
            return res.render("login", {
                error: "Invalid username or password."
            });
        }

        if (!user.is_active) {
            return res.render("login", {
                error: "This account is inactive."
            });
        }

        const passwordMatch = await bcrypt.compare(
            password,
            user.password_hash
        );

        if (!passwordMatch) {
            return res.render("login", {
                error: "Invalid username or password."
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
                expiresIn: "8h"
            }
        );

        res.cookie("token", token, {
            httpOnly: true,
            sameSite: "lax"
        });

        res.redirect("/dashboard");
    } catch (error) {
        console.error(error);

        res.render("login", {
            error: "Something went wrong while logging in."
        });
    }
};

exports.logout = (req, res) => {
    res.clearCookie("token");
    res.redirect("/login");
};