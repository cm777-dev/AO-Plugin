const axios = require('axios');
const cheerio = require('cheerio');

class AnalyzerService {
    async analyzeWebsite(url) {
        try {
            const html = await this.fetchWebsite(url);
            const $ = cheerio.load(html);
            
            const analysis = {
                schema: this.analyzeSchema($),
                metadata: this.analyzeMetadata($),
                accessibility: this.analyzeAccessibility($),
            };
            
            return {
                score: this.calculateScore(analysis),
                analysis,
                recommendations: this.generateRecommendations(analysis),
            };
        } catch (error) {
            throw new Error(`Failed to analyze website: ${error.message}`);
        }
    }

    async fetchWebsite(url) {
        const response = await axios.get(url);
        return response.data;
    }

    analyzeSchema($) {
        const schemas = [];
        $('script[type="application/ld+json"]').each((_, element) => {
            try {
                const schema = JSON.parse($(element).html());
                schemas.push(schema);
            } catch (e) {
                console.error('Invalid JSON-LD schema:', e);
            }
        });
        
        return {
            count: schemas.length,
            types: schemas.map(s => s['@type']),
            score: this.calculateSchemaScore(schemas),
        };
    }

    analyzeMetadata($) {
        const metadata = {
            title: $('title').text(),
            description: $('meta[name="description"]').attr('content'),
            keywords: $('meta[name="keywords"]').attr('content'),
            ogTags: {},
            twitterCards: {},
        };

        $('meta[property^="og:"]').each((_, element) => {
            const property = $(element).attr('property').replace('og:', '');
            metadata.ogTags[property] = $(element).attr('content');
        });

        $('meta[name^="twitter:"]').each((_, element) => {
            const name = $(element).attr('name').replace('twitter:', '');
            metadata.twitterCards[name] = $(element).attr('content');
        });

        return {
            data: metadata,
            score: this.calculateMetadataScore(metadata),
        };
    }

    analyzeAccessibility($) {
        const issues = [];
        
        // Check for alt text in images
        $('img').each((_, element) => {
            if (!$(element).attr('alt')) {
                issues.push('Missing alt text for image');
            }
        });

        // Check for ARIA labels
        $('[role]').each((_, element) => {
            if (!$(element).attr('aria-label')) {
                issues.push('Missing ARIA label for interactive element');
            }
        });

        return {
            issues,
            score: this.calculateAccessibilityScore(issues),
        };
    }

    calculateScore(analysis) {
        const weights = {
            schema: 0.4,
            metadata: 0.4,
            accessibility: 0.2,
        };

        return Math.round(
            analysis.schema.score * weights.schema +
            analysis.metadata.score * weights.metadata +
            analysis.accessibility.score * weights.accessibility
        );
    }

    calculateSchemaScore(schemas) {
        if (schemas.length === 0) return 0;
        // Implement scoring logic based on schema completeness and relevance
        return Math.min(100, schemas.length * 20);
    }

    calculateMetadataScore(metadata) {
        let score = 0;
        
        if (metadata.title) score += 20;
        if (metadata.description) score += 20;
        if (metadata.keywords) score += 10;
        if (Object.keys(metadata.ogTags).length > 0) score += 25;
        if (Object.keys(metadata.twitterCards).length > 0) score += 25;

        return score;
    }

    calculateAccessibilityScore(issues) {
        return Math.max(0, 100 - (issues.length * 10));
    }

    generateRecommendations(analysis) {
        const recommendations = [];

        if (analysis.schema.score < 60) {
            recommendations.push('Add more structured data using JSON-LD schema markup');
        }

        if (analysis.metadata.score < 60) {
            recommendations.push('Improve metadata by adding missing meta tags');
        }

        if (analysis.accessibility.score < 60) {
            recommendations.push('Enhance accessibility by adding ARIA labels and alt text');
        }

        return recommendations;
    }
}

module.exports = new AnalyzerService();
