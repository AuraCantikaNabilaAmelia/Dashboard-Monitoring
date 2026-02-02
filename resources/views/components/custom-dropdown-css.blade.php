<style>
    .custom-dropdown-container {
        position: relative;
        z-index: 100;
        transition: z-index 0.1s;
    }

    .custom-dropdown-container:has(input:where(:checked)) {
        z-index: 1000 !important;
    }

    .status-filter-dropdown {
        border-radius: 20px;
        transition: all 400ms cubic-bezier(0.23, 1, 0.32, 1);
        display: flex;
        flex-direction: column;
        min-height: 56px;
        background: var(--bpkp-dark-glass);
        backdrop-filter: blur(20px);
        position: relative;
        min-width: 180px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: visible;
    }

    .status-filter-dropdown input:where(:checked) ~ .status-filter-list {
        opacity: 1;
        transform: translateY(0) scale(1);
        transition: all 500ms ease;
        padding-bottom: 15px;
        height: auto;
        max-height: 350px;
        visibility: visible;
    }

    .status-filter-dropdown input:where(:not(:checked)) ~ .status-filter-list {
        opacity: 0;
        transform: translateY(-10px);
        user-select: none;
        height: 0px;
        max-height: 0px;
        pointer-events: none;
        transition: all 500ms ease-out;
        visibility: hidden;
    }

    .status-filter-trigger {
        cursor: pointer;
        user-select: none;
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0 1.25rem;
        height: 56px;
        position: relative;
        z-index: 99;
        background: transparent;
        transition: all 300ms;
    }

    /* Shared Arrow Logic */
    .status-filter-trigger:after {
        content: "›";
        rotate: 90deg;
        width: 20px;
        height: 20px;
        color: var(--bpkp-text-light);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        transition: all 350ms ease;
        margin-left: auto;
    }

    .status-filter-dropdown input:where(:checked) + .status-filter-trigger:after {
        rotate: -90deg;
    }

    .status-filter-list {
        position: absolute;
        top: calc(100% + 5px);
        left: -1px;
        width: calc(100% + 2px);
        background: var(--bpkp-dark-glass);
        backdrop-filter: blur(30px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        gap: 2px;
        padding: 8px;
        z-index: 101;
        --w-scrollbar: 6px;
    }

    .status-filter-list::-webkit-scrollbar {
        width: var(--w-scrollbar);
    }
    .status-filter-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .status-filter-list::-webkit-scrollbar-thumb {
        background: transparent;
        border-radius: 10px;
    }
    .status-filter-list:hover::-webkit-scrollbar-thumb {
        background: var(--glass-border);
    }

    .status-filter-listitem {
        list-style: none;
        width: 100%;
    }

    .status-filter-article {
        padding: 0.75rem 1rem;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        width: 100%;
        display: block;
        background: transparent;
        color: var(--bpkp-text-muted);
        transition: all 200ms;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }

    .status-filter-article:hover {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding-left: 1.25rem;
    }

    .status-filter-article.active {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
        border-color: rgba(59, 130, 246, 0.3);
    }
</style>
