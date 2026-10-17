const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const UserProfile = sequelize.define(
    "UserProfile",
    {
        profile_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        user_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            unique: true
        },

        first_name: {
            type: DataTypes.STRING(80),
            allowNull: false
        },

        last_name: {
            type: DataTypes.STRING(80),
            allowNull: false
        },

        phone: {
            type: DataTypes.STRING(30),
            allowNull: true
        }
    },
    {
        tableName: "user_profiles",
        timestamps: false
    }
);

module.exports = UserProfile;