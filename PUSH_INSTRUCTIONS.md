# Push to GitHub & AWS

Your project is already set up with:
- **Git** initialized
- **Remote** `origin` → `https://github.com/rohitash108/laravel-restaurant-pos.git`

---

## 1. Push to GitHub

Run these in the project folder (`csPos`):

```bash
cd /Users/rohit/Documents/csPos

# Stage all files (respects .gitignore)
git add .

# First commit
git commit -m "Initial commit: Laravel Restaurant POS"

# Push to GitHub (creates/updates main branch)
git branch -M main
git push -u origin main
```

If GitHub asks for login, use a **Personal Access Token** (not your password):
- GitHub → Settings → Developer settings → Personal access tokens → Generate new token (repo scope).
- When prompted for password, paste the token.

---

## 2. If you use an empty AWS CodeCommit repo

Add CodeCommit as a second remote and push:

```bash
# Add AWS remote (replace REGION and REPO_NAME with your values)
git remote add aws https://git-codecommit.REGION.amazonaws.com/v1/repos/REPO_NAME

# Or with SSH:
# git remote add aws ssh://git-codecommit.REGION.amazonaws.com/v1/repos/REPO_NAME

# Push to AWS
git push -u aws main
```

**Setup AWS CLI for CodeCommit (one-time):**
- Install AWS CLI and run `aws configure`.
- For HTTPS, use **Git credentials** from IAM → Your user → Security credentials → HTTPS for Git.

---

## 3. Deploying the app on AWS (e.g. EC2)

After code is on GitHub (or CodeCommit), you can deploy to EC2, Elastic Beanstalk, or Laravel Vapor:

1. **EC2**: Clone the repo on the server, run `composer install`, set `.env`, point the web server to `public/`.
2. **Elastic Beanstalk**: Connect your GitHub/CodeCommit repo in the EB console and deploy.
3. **Laravel Vapor**: Connect GitHub in Vapor dashboard and deploy (serverless).

---

## Quick reference

| Task              | Command                    |
|-------------------|----------------------------|
| Check remote      | `git remote -v`            |
| Change remote URL | `git remote set-url origin <new-url>` |
| Push to GitHub    | `git push origin main`     |
| Pull latest       | `git pull origin main`     |
