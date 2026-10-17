const bcrypt = require("bcryptjs");

const {
    User,
    Role
} = require("../models");

exports.index = async (req, res) => {
    try {
        const users = await User.findAll({
            include: [
                {
                    model: Role
                }
            ],
            order: [["user_id", "DESC"]]
        });

        res.render("users/index", {
            user: req.user,
            users
        });

    } catch (error) {
        console.error("USER LIST ERROR:");
        console.error(error);

        res.status(500).send("Error loading users.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const roles = await Role.findAll({
            order: [["role_id", "ASC"]]
        });

        res.render("users/add", {
            user: req.user,
            roles,
            error: null
        });

    } catch (error) {
        console.error("USER ADD FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading add user page.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            role_id,
            username,
            email,
            password,
            is_active
        } = req.body;

        const passwordHash = await bcrypt.hash(password, 10);

        await User.create({
            role_id: Number(role_id),
            username,
            email,
            password_hash: passwordHash,
            is_active: is_active === "on"
        });

        res.redirect("/users");

    } catch (error) {
        console.error("USER CREATE ERROR:");
        console.error(error);

        const roles = await Role.findAll({
            order: [["role_id", "ASC"]]
        });

        res.render("users/add", {
            user: req.user,
            roles,
            error: error.message
        });
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const userRecord = await User.findByPk(req.params.id);

        if (!userRecord) {
            return res.status(404).send("User not found.");
        }

        const roles = await Role.findAll({
            order: [["role_id", "ASC"]]
        });

        res.render("users/edit", {
            user: req.user,
            userRecord,
            roles,
            error: null
        });

    } catch (error) {
        console.error("USER EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading user.");
    }
};

exports.update = async (req, res) => {
    try {
        const userRecord = await User.findByPk(req.params.id);

        if (!userRecord) {
            return res.status(404).send("User not found.");
        }

        const {
            role_id,
            username,
            email,
            password,
            is_active
        } = req.body;

        const updateData = {
            role_id: Number(role_id),
            username,
            email,
            is_active: is_active === "on"
        };

        if (password && password.trim() !== "") {
            updateData.password_hash = await bcrypt.hash(
                password,
                10
            );
        }

        await userRecord.update(updateData);

        res.redirect("/users");

    } catch (error) {
        console.error("USER UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating user: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        const userRecord = await User.findByPk(req.params.id);

        if (!userRecord) {
            return res.status(404).send("User not found.");
        }

        if (Number(userRecord.user_id) === Number(req.user.user_id)) {
            return res.status(400).send(
                "You cannot delete your own account."
            );
        }

        await userRecord.destroy();

        res.redirect("/users");

    } catch (error) {
        console.error("USER DELETE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error deleting user: " + error.message
        );
    }
};