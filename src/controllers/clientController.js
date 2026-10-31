const { Client } = require("../models");

exports.index = async (req, res) => {
    try {
        const clients = await Client.findAll({
            order: [["client_id", "DESC"]]
        });

        res.render("clients/index", {
            user: req.user,
            clients
        });

    } catch (error) {
        console.error("Error loading clients:", error);
        res.status(500).send("Error loading clients.");
    }
};

exports.addForm = (req, res) => {
    res.render("clients/add", {
        user: req.user,
        error: null,
        formData: {}
    });
};

exports.create = async (req, res) => {
    try {
        const { client_name, contact_no, email } = req.body;

        if (!client_name || !client_name.trim()) {
            return res.status(400).render("clients/add", {
                user: req.user,
                error: "Client name is required.",
                formData: req.body
            });
        }

        await Client.create({
            client_name: client_name.trim(),
            contact_no: contact_no?.trim() || null,
            email: email?.trim() || null
        });

        res.redirect("/clients");

    } catch (error) {
        console.error("Error creating client:", error);

        res.status(500).render("clients/add", {
            user: req.user,
            error: "Unable to create client. Please check your information.",
            formData: req.body
        });
    }
};

exports.editForm = async (req, res) => {
    try {
        const client = await Client.findByPk(req.params.id);

        if (!client) {
            return res.status(404).send("Client not found.");
        }

        res.render("clients/edit", {
            user: req.user,
            client,
            error: null
        });

    } catch (error) {
        console.error("Error loading client:", error);
        res.status(500).send("Error loading client.");
    }
};

exports.update = async (req, res) => {
    try {
        const { client_name, contact_no, email } = req.body;

        const client = await Client.findByPk(req.params.id);

        if (!client) {
            return res.status(404).send("Client not found.");
        }

        if (!client_name || !client_name.trim()) {
            return res.status(400).render("clients/edit", {
                user: req.user,
                client: {
                    ...client.toJSON(),
                    ...req.body
                },
                error: "Client name is required."
            });
        }

        await client.update({
            client_name: client_name.trim(),
            contact_no: contact_no?.trim() || null,
            email: email?.trim() || null
        });

        res.redirect("/clients");

    } catch (error) {
        console.error("Error updating client:", error);

        try {
            const client = await Client.findByPk(req.params.id);

            res.status(500).render("clients/edit", {
                user: req.user,
                client: {
                    ...client?.toJSON(),
                    ...req.body
                },
                error: "Unable to update client. Please check your information."
            });

        } catch (secondError) {
            console.error(secondError);
            res.status(500).send("Error updating client.");
        }
    }
};

exports.delete = async (req, res) => {
    try {
        const client = await Client.findByPk(req.params.id);

        if (!client) {
            return res.status(404).send("Client not found.");
        }

        await client.destroy();

        res.redirect("/clients");

    } catch (error) {
        console.error("Error deleting client:", error);
        res.status(500).send(
            "Unable to delete client. It may be associated with existing projects."
        );
    }
};