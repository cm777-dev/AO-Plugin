const mongoose = require('mongoose');

const userSchema = new mongoose.Schema({
    username: {
        type: String,
        required: true,
        unique: true,
    },
    email: {
        type: String,
        required: true,
        unique: true,
    },
    password: {
        type: String,
        required: true,
    },
    isVerified: {
        type: Boolean,
        default: false,
    },
    verificationToken: String,
    subscription: {
        plan: {
            type: String,
            enum: ['free', 'basic', 'pro', 'enterprise'],
            default: 'free',
        },
        status: {
            type: String,
            enum: ['active', 'inactive', 'cancelled'],
            default: 'inactive',
        },
        expiresAt: Date,
    },
    createdAt: {
        type: Date,
        default: Date.now,
    },
});

module.exports = mongoose.model('User', userSchema);
