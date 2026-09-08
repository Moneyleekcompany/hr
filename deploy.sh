#!/bin/bash
# ╔══════════════════════════════════════════════════════════════╗
# ║   Money Leek | Deploy → castle.moneyleek.online              ║
# ║   استضافة Hostinger المشتركة (مش VPS)                        ║
# ╚══════════════════════════════════════════════════════════════╝
#
# الاستخدام:
#   ./deploy.sh check              ← فحص السيرفر الأول (شغّل ده قبل أي حاجة)
#   ./deploy.sh setup              ← أول مرة: يعمل clone للريبو على السيرفر
#   ./deploy.sh "رسالة الـ commit" ← ينشر مع commit جديد
#   ./deploy.sh                    ← ينشر بدون commit
#
# مهم: الاستضافة دي مشتركة — مفيش root ولا sudo ولا systemctl.
# عشان كده مفيش هنا `sudo -u` ولا `apache2ctl graceful` زي سكريبت castle-ops.

set -euo pipefail

SSH_HOST="${DEPLOY_HOST:-46.202.192.55}"
SSH_PORT="${DEPLOY_PORT:-65002}"
SSH_USER="${DEPLOY_USER:-u336950052}"
DOMAIN="${DEPLOY_DOMAIN:-castle.moneyleek.online}"
APP_PATH="${DEPLOY_PATH:-/home/${SSH_USER}/domains/${DOMAIN}/public_html}"
BRANCH="${DEPLOY_BRANCH:-$(git rev-parse --abbrev-ref HEAD)}"
PHP="${DEPLOY_PHP:-php}"

SSH="ssh -p ${SSH_PORT} ${SSH_USER}@${SSH_HOST}"
REPO_URL="$(git config --get remote.origin.url)"

echo ""
echo "🏰  Deploy → ${DOMAIN}"
echo "════════════════════════════════════════"
echo "    ${SSH_USER}@${SSH_HOST}:${SSH_PORT}"
echo "    ${APP_PATH}"
echo "    branch: ${BRANCH}"
echo ""

# ── check ──────────────────────────────────────────────────────
# بيطبع اللي محتاجينه عشان نظبط باقي المتغيرات. مبيغيّرش أي حاجة.
if [ "${1:-}" = "check" ]; then
    $SSH 'echo "host: $(hostname)"; echo "--- domains ---"; ls ~/domains/ 2>/dev/null; \
        echo "--- php ---"; command -v php php8.3 php8.2 composer git 2>/dev/null; \
        php -v 2>/dev/null | head -1; \
        echo "--- app path ---"; ls -la '"$APP_PATH"' 2>&1 | head -15; \
        echo "--- is git repo? ---"; git -C '"$APP_PATH"' remote -v 2>&1 | head -3'
    exit 0
fi

# ── setup ──────────────────────────────────────────────────────
# أول مرة بس: يربط مجلد الموقع بالريبو من غير ما يمسح الملفات الموجودة.
if [ "${1:-}" = "setup" ]; then
    echo "⚙️  ربط ${APP_PATH} بالريبو ${REPO_URL}"
    echo "⚠️  اعمل باك أب للملفات والقاعدة قبل ما تكمل."
    read -p "نكمل؟ (y/n): " c
    [ "$c" != "y" ] && exit 1
    $SSH "set -e; cd '$APP_PATH' && \
        if [ -d .git ]; then echo 'الريبو مربوط بالفعل'; else \
            git init && \
            git remote add origin '$REPO_URL' && \
            git fetch origin '$BRANCH' && \
            git checkout -f -b '$BRANCH' 'origin/$BRANCH'; \
        fi; \
        git remote -v"
    echo "✅ اتربط. شغّل ./deploy.sh عشان تنشر."
    exit 0
fi

# ── 1. commit ──────────────────────────────────────────────────
COMMIT_MSG="${1:-}"
if [ -n "$(git status --porcelain)" ]; then
    if [ -n "$COMMIT_MSG" ]; then
        git add -A
        git commit -m "$COMMIT_MSG"
    else
        echo "⚠️  فيه تغييرات محلية. شغّل: ./deploy.sh \"رسالة الـ commit\""
        read -p "نكمل بدون commit؟ (y/n): " c
        [ "$c" != "y" ] && exit 1
    fi
fi

# ── 2. push ────────────────────────────────────────────────────
echo "📤 push origin ${BRANCH}..."
git push origin "$BRANCH"

# ── 3. deploy ──────────────────────────────────────────────────
# الترتيب هنا مقصود: الـpull و composer والموقع لسه شغال (مفيش تأثير على الزوار).
# بعدين down → بناء الكاش → up، عشان تجميع الـviews يحصل من غير ترافيك —
# ده اللي كان بيسبب أخطاء filemtime() 500 بعد كل ديبلوي على سيرفر castle.
# الخطوات بين down و up بـ';' مش '&&' عشان الموقع يرجع يشتغل حتى لو خطوة فشلت.
echo "🚀 deploying..."
$SSH "cd '$APP_PATH' && \
    git fetch origin '$BRANCH' && \
    git reset --hard 'origin/$BRANCH' && \
    ($PHP composer.phar install --no-dev --optimize-autoloader 2>/dev/null \
        || composer install --no-dev --optimize-autoloader) ; \
    $PHP artisan down --retry=15 || true ; \
    $PHP artisan migrate --force ; \
    $PHP artisan storage:link 2>/dev/null || true ; \
    $PHP artisan config:cache ; \
    $PHP artisan route:cache ; \
    $PHP artisan view:cache ; \
    $PHP artisan event:cache ; \
    $PHP artisan up ; \
    $PHP artisan queue:restart"

echo "✅ deployed → https://${DOMAIN}"
