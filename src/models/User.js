const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const User = sequelize.define(
    "User",
    {
        user_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        role_id: {
            type: DataTypes.INTEGER,
            allowNull: false
        },

        username: {
            type: DataTypes.STRING(80),
            allowNull: false,
            unique: true
        },

        email: {
            type: DataTypes.STRING(120),
            allowNull: false,
            unique: true
        },

        password_hash: {
            type: DataTypes.TEXT,
            allowNull: false
        },

        is_active: {
            type: DataTypes.BOOLEAN,
            allowNull: false,
            defaultValue: true
        },

        created_at: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: DataTypes.NOW
        }
    },
    {
        tableName: "users",
        timestamps: false
    }
);

module.exports = User;