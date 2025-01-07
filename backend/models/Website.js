const mongoose = require('mongoose');

const websiteSchema = new mongoose.Schema({
    url: {
        type: String,
        required: true,
    },
    userId: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true,
    },
    score: {
        overall: Number,
        schema: Number,
        metadata: Number,
        accessibility: Number,
    },
    analysis: {
        schemaMarkup: Object,
        metadata: Object,
        recommendations: [String],
    },
    lastAnalyzed: {
        type: Date,
        default: Date.now,
    },
});

module.exports = mongoose.model('Website', websiteSchema);
