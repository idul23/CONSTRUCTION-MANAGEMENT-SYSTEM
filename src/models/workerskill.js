const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const WorkerSkill = sequelize.define(
    "WorkerSkill",
    {
        worker_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            allowNull: false
        },

        skill_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            allowNull: false
        },

        proficiency: {
            type: DataTypes.STRING(50),
            allowNull: true
        },

        years_exp: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },

        certified_date: {
            type: DataTypes.DATEONLY,
            allowNull: true
        }
    },
    {
        tableName: "worker_skills",
        timestamps: false
    }
);

module.exports = WorkerSkill;