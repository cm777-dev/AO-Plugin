const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

const app = express();
const PORT = process.env.PORT || 5000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// In-memory storage (replace with database in production)
const users = [];
const subscriptions = [
    { id: 1, name: 'Basic', price: 9.99 },
    { id: 2, name: 'Pro', price: 19.99 },
    { id: 3, name: 'Enterprise', price: 49.99 }
];

// Auth middleware
const authenticateToken = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) return res.sendStatus(401);

    jwt.verify(token, 'your-secret-key', (err, user) => {
        if (err) return res.sendStatus(403);
        req.user = user;
        next();
    });
};

// Routes
app.post('/register', async (req, res) => {
    try {
        const { username, password, email } = req.body;
        
        // Check if user exists
        if (users.find(u => u.username === username)) {
            return res.status(400).json({ message: 'Username already exists' });
        }

        // Hash password
        const hashedPassword = await bcrypt.hash(password, 10);
        
        // Create user
        const user = {
            id: users.length + 1,
            username,
            password: hashedPassword,
            email
        };
        
        users.push(user);
        res.status(201).json({ message: 'User created successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Error creating user' });
    }
});

app.post('/login', async (req, res) => {
    try {
        const { username, password } = req.body;
        const user = users.find(u => u.username === username);

        if (!user) {
            return res.status(400).json({ message: 'User not found' });
        }

        const validPassword = await bcrypt.compare(password, user.password);
        if (!validPassword) {
            return res.status(400).json({ message: 'Invalid password' });
        }

        const token = jwt.sign({ id: user.id, username: user.username }, 'your-secret-key');
        res.json({ token });
    } catch (error) {
        res.status(500).json({ message: 'Error logging in' });
    }
});

// Protected routes
app.get('/subscriptions', authenticateToken, (req, res) => {
    res.json(subscriptions);
});

// Agent readiness scoring endpoint
app.post('/score-website', authenticateToken, (req, res) => {
    const { url } = req.body;
    // Mock scoring logic
    const score = Math.floor(Math.random() * 100);
    res.json({ score, recommendations: ['Add schema markup', 'Improve metadata'] });
});

// Metadata generation endpoint
app.post('/generate-metadata', authenticateToken, (req, res) => {
    const { websiteData } = req.body;
    // Mock metadata generation
    const metadata = {
        title: websiteData.title || 'Sample Title',
        description: websiteData.description || 'Sample Description',
        schema: {
            '@context': 'https://schema.org',
            '@type': 'Website',
            name: websiteData.title,
            description: websiteData.description
        }
    };
    res.json(metadata);
});

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
