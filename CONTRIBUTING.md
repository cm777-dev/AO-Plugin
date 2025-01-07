# Contributing to Agent Optimization Pro

First off, thank you for considering contributing to Agent Optimization Pro! It's people like you that make Agent Optimization Pro such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

* **Use a clear and descriptive title**
* **Describe the exact steps which reproduce the problem**
* **Provide specific examples to demonstrate the steps**
* **Describe the behavior you observed after following the steps**
* **Explain which behavior you expected to see instead and why**
* **Include screenshots if possible**

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

* **Use a clear and descriptive title**
* **Provide a step-by-step description of the suggested enhancement**
* **Provide specific examples to demonstrate the steps**
* **Describe the current behavior and explain the behavior you expected to see instead**
* **Explain why this enhancement would be useful**

### Pull Requests

* Fill in the required template
* Follow the WordPress Coding Standards
* Include appropriate test cases
* End files with a newline
* Update the documentation accordingly

## Development Process

1. Fork the repo
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run the tests
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Local Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/AO-Plugin.git

# Install dependencies
composer install

# Run tests
composer test
```

### Coding Standards

We follow the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/):

* Use tabs for indentation
* Use proper spacing around operators
* Add comments for functions and complex code blocks
* Follow WordPress naming conventions

### Testing

* Write unit tests for new features
* Ensure all tests pass before submitting PR
* Include integration tests where appropriate
* Test across different PHP versions (7.4+)

```bash
# Run PHPUnit tests
composer test

# Run PHP CodeSniffer
composer phpcs

# Fix coding standards automatically
composer phpcbf
```

## Documentation

* Update the README.md if needed
* Add JSDoc comments for JavaScript functions
* Update PHPDoc blocks for PHP functions
* Update the [GitBook documentation](https://cm777.gitbook.io/cm777-docs) if necessary

## Versioning

We use [SemVer](http://semver.org/) for versioning:

* MAJOR version for incompatible API changes
* MINOR version for backwards-compatible functionality additions
* PATCH version for backwards-compatible bug fixes

## Community

* Join our [Discord community](https://discord.gg/your-invite-link)
* Follow us on [Twitter](https://twitter.com/your-twitter)
* Read our [blog](https://your-blog.com)

## Recognition

Contributors will be recognized in:

* The project's README.md
* Our documentation
* Release notes

## Questions?

* Check our [documentation](https://cm777.gitbook.io/cm777-docs)
* Join our community channels
* Create a GitHub issue

Thank you for contributing to Agent Optimization Pro! 🎉
