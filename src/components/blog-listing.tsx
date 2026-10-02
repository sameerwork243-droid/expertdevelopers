"use client";

import { useMemo, useState } from "react";

const PER_PAGE = 12;

function ArrowDownIcon({ stroke = "#07234B" }: { stroke?: string }) {
  return (
    <svg className="arrow-down-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
      <path d="M13 5.5L8 10.5L3 5.5" stroke={stroke} strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function PageArrow({ dir }: { dir: "left" | "right" }) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
      {dir === "left" ? (
        <path d="M15.75 19.5L8.25 12L15.75 4.5" stroke="#07234B" strokeLinecap="round" strokeLinejoin="round" />
      ) : (
        <path d="M8.25 4.5L15.75 12L8.25 19.5" stroke="#07234B" strokeLinecap="round" strokeLinejoin="round" />
      )}
    </svg>
  );
}

function blogPageNumbers(page: number, total: number): (number | "…")[] {
  if (total <= 8) return Array.from({ length: total }, (_, i) => i + 1);
  if (page <= 6) return [1, 2, 3, 4, 5, 6, "…", total];
  if (page >= total - 4) {
    const out: (number | "…")[] = [1, "…"];
    for (let i = total - 4; i <= total; i++) out.push(i);
    return out;
  }
  return [1, "…", page - 2, page - 1, page, page + 1, page + 2, "…", total];
}

export function BlogListing({ posts }: { posts: any[] }) {
  const [query, setQuery] = useState("");
  const [category, setCategory] = useState("All Categories");
  const [catOpen, setCatOpen] = useState(false);
  const [page, setPage] = useState(1);

  const categories = useMemo(() => {
    const seen = new Set<string>();
    for (const p of posts) if (p.category) seen.add(p.category);
    return ["All Categories", ...Array.from(seen)];
  }, [posts]);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    return posts.filter((p) => {
      if (category !== "All Categories" && p.category !== category) return false;
      if (q && !`${p.title} ${p.category}`.toLowerCase().includes(q)) return false;
      return true;
    });
  }, [posts, query, category]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
  const safePage = Math.min(page, totalPages);
  const pageItems = filtered.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  const go = (p: number) => {
    setPage(Math.max(1, Math.min(totalPages, p)));
  };

  return (
    <div className="blog-listing-wrap listing-wrap">
      <div className="category-section-wrap category-section">
        <div className="category-section container">
          <div className="max-filter">
            <div className="search-box-comm">
              <input
                className="form-control search"
                placeholder="Search by keyword"
                value={query}
                onChange={(e) => {
                  setQuery(e.target.value);
                  setPage(1);
                }}
              />
              <span>
                <i className="icon grey-search-icon"></i>
              </span>
            </div>
            <div className="react-select-wrap">
              <div className={"react-select css-b62m3t-container" + (catOpen ? " react-select--is-open" : "")}>
                <div
                  className={"react-select__control css-14qho42-control" + (catOpen ? " react-select__control--menu-is-open" : "")}
                  onClick={() => setCatOpen(!catOpen)}
                >
                  <div className="react-select__value-container react-select__value-container--has-value css-hlgwow">
                    <div className="react-select__single-value css-1ubv46r-singleValue">{category}</div>
                  </div>
                  <div className="react-select__indicators css-1wy0on6">
                    <span className="react-select__indicator-separator css-1uei4ir-indicatorSeparator"></span>
                    <div className="dropdown-indicator react-select__indicator react-select__dropdown-indicator css-15ctyzv-indicatorContainer" aria-hidden="true">
                      <ArrowDownIcon />
                    </div>
                  </div>
                </div>
                {catOpen && (
                  <div className="react-select__menu">
                    <div className="react-select__menu-list">
                      {categories.map((c) => (
                        <div
                          key={c}
                          className={"react-select__option" + (c === category ? " react-select__option--is-selected" : "")}
                          onClick={() => {
                            setCategory(c);
                            setPage(1);
                            setCatOpen(false);
                          }}
                        >
                          {c}
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="blog-listing-container container">
        <div className="blog-category-select-section"></div>
        <div className="blog-listing-section">
          {pageItems.map((b, i) => (
            <div className="news-card-wrapper" key={i}>
              <div className="news-card">
                <div className="img-section-wrap img-zoom">
                  <a className="img-section" href={`/blog/${b.slug}/`}>
                    {b.image && <img loading="lazy" src={b.image} alt={b.title} />}
                    {b.category && <p className="img-tag">{b.category}</p>}
                  </a>
                </div>
                <a className="title" href={`/blog/${b.slug}/`}>
                  {b.title}
                </a>
                <p className="date">{b.date}</p>
              </div>
            </div>
          ))}
        </div>
        {totalPages > 1 && (
          <nav className="pagination-wrapper search-pagination-wrapper">
            <div>
              <div className="pagination-container">
                <button
                  type="button"
                  className={"button button-white pagination-button button-back" + (safePage === 1 ? " button-disabled" : "")}
                  aria-label="Previous page"
                  disabled={safePage === 1}
                  onClick={() => go(safePage - 1)}
                >
                  <PageArrow dir="left" />
                </button>
                <div className="pagination-numbers">
                  {blogPageNumbers(safePage, totalPages).map((n, i) =>
                    n === "…" ? (
                      <span className="pagination-dots" key={`d${i}`}>
                        ...
                      </span>
                    ) : (
                      <button
                        type="button"
                        className={"pagination-number" + (n === safePage ? " active" : "")}
                        aria-current={n === safePage ? "page" : undefined}
                        key={n}
                        onClick={() => go(n as number)}
                      >
                        {n}
                      </button>
                    ),
                  )}
                </div>
                <button
                  type="button"
                  className={"button button-white pagination-button button-next" + (safePage >= totalPages ? " button-disabled" : "")}
                  aria-label="Next page"
                  disabled={safePage >= totalPages}
                  onClick={() => go(safePage + 1)}
                >
                  <PageArrow dir="right" />
                </button>
              </div>
            </div>
          </nav>
        )}
      </div>
    </div>
  );
}