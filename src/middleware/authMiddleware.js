const jwt = require("jsonwebtoken");

exports.requireAuth = (req, res, next) => {
    const token = req.cookies?.token;

    if (!token) {
        return res.redirect("/login");
    }

    try {
        const decoded = jwt.verify(
            token,
            process.env.JWT_SECRET
        );

        req.user = decoded;

        next();

    } catch (error) {
        res.clearCookie("token");

        return res.redirect("/login");
    }
};

exports.requireRole = (...allowedRoles) => {
    return (req, res, next) => {

        if (!req.user) {
            return res.redirect("/login");
        }

        if (!allowedRoles.includes(req.user.role_name)) {
            return res.status(403).send("Access denied.");
        }

        next();
    };
};