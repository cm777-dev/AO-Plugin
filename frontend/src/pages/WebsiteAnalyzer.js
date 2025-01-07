import React, { useState } from 'react';
import {
  Container,
  Paper,
  TextField,
  Button,
  Typography,
  makeStyles,
  CircularProgress,
  Box,
} from '@material-ui/core';
import axios from 'axios';

const useStyles = makeStyles((theme) => ({
  container: {
    marginTop: theme.spacing(4),
  },
  paper: {
    padding: theme.spacing(3),
    marginBottom: theme.spacing(3),
  },
  form: {
    display: 'flex',
    flexDirection: 'column',
    gap: theme.spacing(2),
  },
  results: {
    marginTop: theme.spacing(3),
  },
  score: {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: theme.spacing(2),
    marginBottom: theme.spacing(2),
  },
  recommendations: {
    marginTop: theme.spacing(2),
  },
}));

function WebsiteAnalyzer() {
  const classes = useStyles();
  const [url, setUrl] = useState('');
  const [loading, setLoading] = useState(false);
  const [results, setResults] = useState(null);
  const [error, setError] = useState('');

  const handleAnalyze = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setResults(null);

    try {
      const token = localStorage.getItem('token');
      const response = await axios.post(
        'http://localhost:5000/score-website',
        { url },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      setResults(response.data);
    } catch (error) {
      setError(error.response?.data?.message || 'An error occurred');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container className={classes.container}>
      <Paper className={classes.paper}>
        <Typography variant="h5" gutterBottom>
          Website Analyzer
        </Typography>
        <form onSubmit={handleAnalyze} className={classes.form}>
          <TextField
            label="Website URL"
            variant="outlined"
            value={url}
            onChange={(e) => setUrl(e.target.value)}
            required
            fullWidth
          />
          <Button
            type="submit"
            variant="contained"
            color="primary"
            disabled={loading}
          >
            {loading ? 'Analyzing...' : 'Analyze Website'}
          </Button>
        </form>

        {loading && (
          <Box className={classes.score}>
            <CircularProgress />
          </Box>
        )}

        {error && (
          <Typography color="error" className={classes.results}>
            {error}
          </Typography>
        )}

        {results && (
          <div className={classes.results}>
            <Box className={classes.score}>
              <Typography variant="h4" color="primary">
                Score: {results.score}
              </Typography>
            </Box>
            <Typography variant="h6">Recommendations:</Typography>
            <ul className={classes.recommendations}>
              {results.recommendations.map((rec, index) => (
                <li key={index}>
                  <Typography>{rec}</Typography>
                </li>
              ))}
            </ul>
          </div>
        )}
      </Paper>
    </Container>
  );
}

export default WebsiteAnalyzer;
