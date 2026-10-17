const {
    UserProfile,
    User
} = require("../models");

exports.index = async (req, res) => {
    try {
        const profiles = await UserProfile.findAll({
            include: [
                {
                    model: User,
                    attributes: ["username", "email"]
                }
            ],
            order: [
                ["profile_id", "DESC"]
            ]
        });

        res.render("user-profiles/index", {
            user: req.user,
            profiles
        });

    } catch (error) {
        console.error("USER PROFILES LIST ERROR:");
        console.error(error);

        res.status(500).send("Error loading user profiles.");
    }
};

exports.showAddForm = async (req, res) => {
    try {
        const users = await User.findAll({
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("user-profiles/add", {
            user: req.user,
            users,
            error: null
        });

    } catch (error) {
        console.error("USER PROFILE ADD FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading user profile form.");
    }
};

exports.create = async (req, res) => {
    try {
        const {
            user_id,
            first_name,
            last_name,
            phone
        } = req.body;

        await UserProfile.create({
            user_id: Number(user_id),
            first_name,
            last_name,
            phone: phone || null
        });

        res.redirect("/user-profiles");

    } catch (error) {
        console.error("USER PROFILE CREATE ERROR:");
        console.error(error);

        const users = await User.findAll({
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("user-profiles/add", {
            user: req.user,
            users,
            error: error.message
        });
    }
};

exports.showEditForm = async (req, res) => {
    try {
        const profile = await UserProfile.findByPk(
            req.params.id
        );

        if (!profile) {
            return res.status(404).send("User profile not found.");
        }

        const users = await User.findAll({
            order: [
                ["username", "ASC"]
            ]
        });

        res.render("user-profiles/edit", {
            user: req.user,
            profile,
            users,
            error: null
        });

    } catch (error) {
        console.error("USER PROFILE EDIT FORM ERROR:");
        console.error(error);

        res.status(500).send("Error loading user profile.");
    }
};

exports.update = async (req, res) => {
    try {
        const profile = await UserProfile.findByPk(
            req.params.id
        );

        if (!profile) {
            return res.status(404).send("User profile not found.");
        }

        const {
            user_id,
            first_name,
            last_name,
            phone
        } = req.body;

        const newUserId = Number(user_id);

        if (newUserId !== Number(profile.user_id)) {
            const existing = await UserProfile.findOne({
                where: {
                    user_id: newUserId
                }
            });

            if (existing) {
                return res.status(400).send(
                    "This user already has a profile."
                );
            }
        }

        await profile.update({
            user_id: newUserId,
            first_name,
            last_name,
            phone: phone || null
        });

        res.redirect("/user-profiles");

    } catch (error) {
        console.error("USER PROFILE UPDATE ERROR:");
        console.error(error);

        res.status(500).send(
            "Error updating user profile: " + error.message
        );
    }
};

exports.delete = async (req, res) => {
    try {
        await UserProfile.destroy({
            where: {
                profile_id: req.params.id
            }
        });

        res.redirect("/user-profiles");

    } catch (error) {
        console.error("USER PROFILE DELETE ERROR:");
        console.error(error);

        res.status(500).send("Error deleting user profile.");
    }
};