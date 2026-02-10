<style>
    .custom-dropdown-container {
        position: relative;
        z-index: 100;
        transition: z-index 0.1s;
    }

    .custom-dropdown-container:has(input:where(:checked)) {
        z-index: 9999 !important;
    }

    .status-filter-dropdown {
        border-radius: 12px;
        transition: all 400ms cubic-bezier(0.23, 1, 0.32, 1);
        display: flex;
        flex-direction: column;
        min-height: 48px;
        background: transparent;
        position: relative;
        min-width: 180px;
        overflow: visible;
    }

    .status-filter-dropdown input:where(:checked) ~ .status-filter-list {
        opacity: 1;
        transform: translateY(0) scale(1);
        transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
        padding: 8px;
        height: auto;
        max-height: 350px;
        visibility: visible;
        pointer-events: auto;
    }

    .status-filter-dropdown input:where(:not(:checked)) ~ .status-filter-list {
        opacity: 0;
        transform: translateY(-8px) scale(0.98);
        user-select: none;
        height: 0px;
        max-height: 0px;
        pointer-events: none;
        transition: all 200ms ease-out;
        visibility: hidden;
        padding: 0;
    }

    .status-filter-trigger {
        cursor: pointer;
        user-select: none;
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0 1rem;
        height: 48px;
        position: relative;
        z-index: 99;
        background: transparent;
        transition: all 300ms;
    }

.status-filter-trigger:after {
        content: "";
        width: 16px;
        height: 16px;
        margin-left: auto;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
        transition: all 300ms ease;
    }

    .status-filter-dropdown input:where(:checked) + .status-filter-trigger:after {
        transform: rotate(180deg);
    }

    .status-filter-list {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 100%;
        background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05) inset;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
        gap: 4px;
        z-index: 9999;
        --w-scrollbar: 6px;
    }

[data-theme="light"] .status-filter-list {
        background: rgba(255, 255, 255, 0.98);
        border-color: rgba(0, 0, 0, 0.1);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
    }

    .status-filter-list::-webkit-scrollbar {
        width: var(--w-scrollbar);
    }
    .status-filter-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .status-filter-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 10px;
    }
    .status-filter-list:hover::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.5);
    }

    .status-filter-listitem {
        list-style: none;
        width: 100%;
    }

    .status-filter-article {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        width: 100%;
        display: flex;
        align-items: center;
        background: transparent;
        color: #94a3b8;
        transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        text-decoration: none;
        cursor: pointer;
    }

    .status-filter-article:hover {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        padding-left: 20px;
    }

    .status-filter-article.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(99, 102, 241, 0.15));
        color: #60a5fa;
        font-weight: 700;
    }

[data-theme="light"] .status-filter-article {
        color: #64748b;
    }

    [data-theme="light"] .status-filter-article:hover {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    [data-theme="light"] .status-filter-article.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(99, 102, 241, 0.1));
        color: #3b82f6;
    }
</style>
