// Initializes scroll-triggered animations for elements with the
// `animate-on-scroll` class. Elements can specify the animation
// class via `data-anim` (default: `animate-fade-in-up`) and
// optional `data-delay` in seconds.

export function initScrollAnimations() {
    const options: IntersectionObserverInit = {
        root: null,
        rootMargin: '0px 0px -10% 0px',
        threshold: 0.08,
    };

    const applyAnimation = (el: Element) => {
        if (!(el instanceof HTMLElement)) return;
        const anim = el.dataset.anim || 'animate-fade-in-up';
        const delay = el.dataset.delay || '0';
        el.style.animationDelay = `${parseFloat(delay)}s`;
        // ensure element is initially hidden for the animation
        if (!el.classList.contains(anim)) {
            el.classList.add('will-animate');
        }
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target as HTMLElement;
                const anim = el.dataset.anim || 'animate-fade-in-up';
                el.classList.add(anim);
                el.classList.remove('will-animate');
                obs.unobserve(entry.target);
            }
        });
    }, options);

    const observeAll = (root: ParentNode = document) => {
        const els = (root as Document).querySelectorAll?.('.animate-on-scroll') || [];
        els.forEach((el) => {
            applyAnimation(el);
            observer.observe(el);
        });
    };

    // Observe initial DOM
    observeAll(document);

    // Watch for dynamically added nodes that include `.animate-on-scroll`
    const mo = new MutationObserver((mutations) => {
        mutations.forEach((m) => {
            m.addedNodes.forEach((node) => {
                if (!(node instanceof Element)) return;
                if (node.matches && node.matches('.animate-on-scroll')) {
                    applyAnimation(node);
                    observer.observe(node);
                }
                // also check descendants
                node.querySelectorAll && node.querySelectorAll('.animate-on-scroll').forEach((el) => {
                    applyAnimation(el);
                    observer.observe(el);
                });
            });
        });
    });

    mo.observe(document.documentElement || document.body, { childList: true, subtree: true });

    // return cleanup function if ever needed
    return () => {
        observer.disconnect();
        mo.disconnect();
    };
}
