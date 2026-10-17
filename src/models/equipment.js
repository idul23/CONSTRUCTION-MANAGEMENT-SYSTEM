const { DataTypes } = require("sequelize");
const sequelize = require("../config/database");

const Equipment = sequelize.define(
    "Equipment",
    {
        equipment_id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },

        name: {
            type: DataTypes.STRING(120),
            allowNull: false
        },

        equip_type: {
            type: DataTypes.STRING(80),
            allowNull: true
        },

        serial_no: {
            type: DataTypes.STRING(100),
            allowNull: false,
            unique: true
        },

        daily_rate: {
            type: DataTypes.DECIMAL(12, 2),
            allowNull: false,
            defaultValue: 0
        },

        condition: {
            type: DataTypes.STRING(50),
            allowNull: false,
            defaultValue: "good"
        }
    },
    {
        tableName: "equipment",
        timestamps: false
    }
);

module.exports = Equipment;