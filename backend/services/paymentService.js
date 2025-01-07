const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);
const User = require('../models/User');

class PaymentService {
    async createCustomer(user) {
        const customer = await stripe.customers.create({
            email: user.email,
            metadata: {
                userId: user._id.toString(),
            },
        });
        return customer;
    }

    async createSubscription(userId, priceId) {
        const user = await User.findById(userId);
        if (!user) {
            throw new Error('User not found');
        }

        let customer;
        if (!user.stripeCustomerId) {
            customer = await this.createCustomer(user);
            user.stripeCustomerId = customer.id;
            await user.save();
        }

        const subscription = await stripe.subscriptions.create({
            customer: user.stripeCustomerId,
            items: [{ price: priceId }],
            payment_behavior: 'default_incomplete',
            expand: ['latest_invoice.payment_intent'],
        });

        return {
            subscriptionId: subscription.id,
            clientSecret: subscription.latest_invoice.payment_intent.client_secret,
        };
    }

    async cancelSubscription(subscriptionId) {
        return await stripe.subscriptions.del(subscriptionId);
    }

    async updateSubscription(subscriptionId, newPriceId) {
        const subscription = await stripe.subscriptions.retrieve(subscriptionId);
        
        return await stripe.subscriptions.update(subscriptionId, {
            items: [{
                id: subscription.items.data[0].id,
                price: newPriceId,
            }],
        });
    }

    async handleWebhook(event) {
        switch (event.type) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                const subscription = event.data.object;
                await this.updateUserSubscription(subscription);
                break;
            
            case 'customer.subscription.deleted':
                const cancelledSubscription = event.data.object;
                await this.cancelUserSubscription(cancelledSubscription);
                break;
        }
    }

    async updateUserSubscription(subscription) {
        const user = await User.findOne({
            stripeCustomerId: subscription.customer,
        });

        if (!user) return;

        user.subscription = {
            status: subscription.status,
            plan: this.getPlanFromPriceId(subscription.items.data[0].price.id),
            expiresAt: new Date(subscription.current_period_end * 1000),
        };

        await user.save();
    }

    async cancelUserSubscription(subscription) {
        const user = await User.findOne({
            stripeCustomerId: subscription.customer,
        });

        if (!user) return;

        user.subscription = {
            status: 'cancelled',
            plan: 'free',
            expiresAt: new Date(),
        };

        await user.save();
    }

    getPlanFromPriceId(priceId) {
        // Map your Stripe price IDs to plan names
        const priceIdToPlan = {
            'price_basic': 'basic',
            'price_pro': 'pro',
            'price_enterprise': 'enterprise',
        };
        return priceIdToPlan[priceId] || 'free';
    }
}

module.exports = new PaymentService();
