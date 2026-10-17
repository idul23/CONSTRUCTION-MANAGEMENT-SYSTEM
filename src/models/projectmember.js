const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const ProjectMember = sequelize.define(
    "ProjectMember",
    {
        project_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            allowNull: false
        },

        user_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            allowNull: false
        },

        project_role: {
            type: DataTypes.STRING(80),
            allowNull: false
        },

        assigned_date: {
            type: DataTypes.DATEONLY,
            allowNull: false,
            defaultValue: DataTypes.NOW
        },

        is_active: {
            type: DataTypes.BOOLEAN,
            allowNull: false,
            defaultValue: true
        }
    },
    {
        tableName: "project_members",
        timestamps: false
    }
);

module.exports = ProjectMember;