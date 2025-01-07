import React, { useState, useEffect } from 'react';
import {
  Container,
  Grid,
  Paper,
  Typography,
  Button,
  makeStyles,
} from '@material-ui/core';
import axios from 'axios';

const useStyles = makeStyles((theme) => ({
  container: {
    marginTop: theme.spacing(4),
  },
  paper: {
    padding: theme.spacing(3),
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    height: '100%',
  },
  price: {
    fontSize: '2rem',
    marginBottom: theme.spacing(2),
  },
  features: {
    margin: theme.spacing(2, 0),
  },
  button: {
    marginTop: 'auto',
  },
}));

function Subscriptions() {
  const classes = useStyles();
  const [subscriptions, setSubscriptions] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchSubscriptions = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://localhost:5000/subscriptions', {
          headers: { Authorization: `Bearer ${token}` },
        });
        setSubscriptions(response.data);
      } catch (error) {
        setError(error.response?.data?.message || 'An error occurred');
      }
    };

    fetchSubscriptions();
  }, []);

  const handleSubscribe = (planId) => {
    // Mock subscription handling
    console.log(`Subscribing to plan ${planId}`);
  };

  return (
    <Container className={classes.container}>
      <Typography variant="h4" gutterBottom>
        Subscription Plans
      </Typography>
      {error && (
        <Typography color="error" gutterBottom>
          {error}
        </Typography>
      )}
      <Grid container spacing={3}>
        {subscriptions.map((plan) => (
          <Grid item xs={12} sm={6} md={4} key={plan.id}>
            <Paper className={classes.paper}>
              <Typography variant="h5" gutterBottom>
                {plan.name}
              </Typography>
              <Typography className={classes.price} color="primary">
                ${plan.price}/mo
              </Typography>
              <div className={classes.features}>
                <Typography>Feature 1</Typography>
                <Typography>Feature 2</Typography>
                <Typography>Feature 3</Typography>
              </div>
              <Button
                variant="contained"
                color="primary"
                className={classes.button}
                onClick={() => handleSubscribe(plan.id)}
              >
                Subscribe
              </Button>
            </Paper>
          </Grid>
        ))}
      </Grid>
    </Container>
  );
}

export default Subscriptions;
