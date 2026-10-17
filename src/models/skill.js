const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Skill = sequelize.define(
    "Skill",
    {
        skill_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        skill_name: {
            type: DataTypes.STRING(100),
            allowNull: false,
            unique: true
        }
    },
    {
        tableName: "skills",
        timestamps: false
    }
);

module.exports = Skill;