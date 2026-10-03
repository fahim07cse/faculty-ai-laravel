# GitHub setup

## First push

```bash
git init
git add .
git commit -m "Initial Laravel Faculty AI project"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/faculty-ai-laravel.git
git push -u origin main
```

## Important

Make sure `.env` is not tracked:

```bash
git status
git check-ignore .env
```

If `.env` was already staged, remove it from Git tracking:

```bash
git rm --cached .env
git commit -m "Remove environment file"
```
