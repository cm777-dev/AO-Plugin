import React from 'react';
import {
  Container,
  Grid,
  Paper,
  Typography,
  makeStyles,
  Button,
} from '@material-ui/core';
import { useNavigate } from 'react-router-dom';

const useStyles = makeStyles((theme) => ({
  container: {
    paddingTop: theme.spacing(4),
    paddingBottom: theme.spacing(4),
  },
  paper: {
    padding: theme.spacing(2),
    display: 'flex',
    flexDirection: 'column',
    height: '100%',
  },
  fixedHeight: {
    height: 240,
  },
  button: {
    marginTop: theme.spacing(2),
  },
}));

function Dashboard() {
  const classes = useStyles();
  const navigate = useNavigate();

  return (
    <Container maxWidth="lg" className={classes.container}>
      <Grid container spacing={3}>
        <Grid item xs={12} md={8} lg={9}>
          <Paper className={classes.paper}>
            <Typography component="h2" variant="h6" color="primary" gutterBottom>
              Welcome to Agent Optimization Platform
            </Typography>
            <Typography paragraph>
              Optimize your website for AI agents and improve your online presence.
            </Typography>
            <Button
              variant="contained"
              color="primary"
              className={classes.button}
              onClick={() => navigate('/analyzer')}
            >
              Analyze Website
            </Button>
          </Paper>
        </Grid>
        <Grid item xs={12} md={4} lg={3}>
          <Paper className={classes.paper}>
            <Typography component="h2" variant="h6" color="primary" gutterBottom>
              Quick Stats
            </Typography>
            <Typography>Websites Analyzed: 0</Typography>
            <Typography>Average Score: N/A</Typography>
            <Typography>Subscription Status: Active</Typography>
          </Paper>
        </Grid>
        <Grid item xs={12}>
          <Paper className={classes.paper}>
            <Typography component="h2" variant="h6" color="primary" gutterBottom>
              Recent Activity
            </Typography>
            <Typography>No recent activity</Typography>
          </Paper>
        </Grid>
      </Grid>
    </Container>
  );
}

export default Dashboard;
