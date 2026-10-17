const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Client = sequelize.define(
    "Client",
    {
        client_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        client_name: {
            type: DataTypes.STRING(120),
            allowNull: false
        },

        contact_no: {
            type: DataTypes.STRING(30),
            allowNull: true
        },

        email: {
            type: DataTypes.STRING(120),
            allowNull: true
        }
    },
    {
        tableName: "clients",
        timestamps: false
    }
);

module.exports = Client;