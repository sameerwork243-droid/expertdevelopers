"use client";

import { useMemo } from "react";

function Chevron({ dir = "right" }: { dir?: "left" | "right" }) {
  return (
    <svg
      aria-hidden="true"
      className="bp40-chevron"
      width="16"
      height="16"
      viewBox="0 0 16 16"
      fill="none"
      style={{ transform: dir === "left" ? "rotate(180deg)" : undefined }}
    >
      <path d="M6 3L11 8L6 13" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function range(start: number, end: number): number[] {
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
}

/**
 * Square 40x40 pagination, 16px gap, 6px corner radius.
 * Active page = solid #0F2A5C; inactive = white with #E5E7EB border;
 * trailing chevron ("next") button matches the inactive style.
 *
 * Example (total = 2, current = 1, withPrev = false):
 *   [1] [2] [>]
 */
export function Pagination({
  total,
  current,
  onChange,
  withPrev = false,
  siblingCount = 1,
}: {
  total: number;
  current: number;
  onChange: (page: number) => void;
  withPrev?: boolean;
  siblingCount?: number;
}) {
  const safeCurrent = Math.min(Math.max(1, current), Math.max(1, total));

  const pages = useMemo<Array<number | "…">>(() => {
    if (total <= 7) return range(1, total);
    const start = Math.max(1, safeCurrent - siblingCount);
    const end = Math.min(total, safeCurrent + siblingCount);
    const items: Array<number | "…"> = [];
    if (start > 1) {
      items.push(1);
      if (start > 2) items.push("…");
    }
    for (let i = start; i <= end; i++) items.push(i);
    if (end < total) {
      if (end < total - 1) items.push("…");
      items.push(total);
    }
    return items;
  }, [total, safeCurrent, siblingCount]);

  const go = (n: number) => {
    if (n >= 1 && n <= total) onChange(n);
  };

  return (
    <nav className="bp40" aria-label="Pagination">
      {withPrev && (
        <button
          type="button"
          className="bp40-btn"
          aria-label="Previous page"
          disabled={safeCurrent === 1}
          onClick={() => go(safeCurrent - 1)}
        >
          <Chevron dir="left" />
        </button>
      )}
      {pages.map((p, i) =>
        typeof p === "number" ? (
          <button
            type="button"
            key={p}
            className={"bp40-btn" + (p === safeCurrent ? " is-active" : "")}
            aria-current={p === safeCurrent ? "page" : undefined}
            onClick={() => go(p)}
          >
            {p}
          </button>
        ) : (
          <span className="bp40-ellipsis" key={"ellipsis-" + i}>
            …
          </span>
        )
      )}
      <button
        type="button"
        className="bp40-btn bp40-next"
        aria-label="Next page"
        disabled={safeCurrent === total}
        onClick={() => go(safeCurrent + 1)}
      >
        <Chevron dir="right" />
      </button>
    </nav>
  );
}