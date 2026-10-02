export function priceFmt(n: number | null | undefined): string {
  if (n == null) return "";
  return n.toLocaleString("en-US");
}

export function PriceFmt({ value, qualifier }: { value?: number | null; qualifier?: string | null }) {
  if (value == null) return <>{qualifier || ""}</>;
  return (
    <>
      {qualifier ? `${qualifier} ` : "AED "}
      {value.toLocaleString("en-US")}
    </>
  );
}

export function addressOf(hit: any): string {
  if (hit.display_address) return hit.display_address;
  const a = hit.address_full || {};
  return [a.address2, a.address3, a.address4].filter(Boolean).join(", ") || a.area || "";
}

export function propLink(hit: any): string {
  const t = String(hit.search_type || "").toLowerCase();
  const base = t.includes("rent") || t.includes("letting") ? "/let/" : "/buy/";
  return `${base}${hit.slug || ""}${hit.id}/`;
}

const SITE_BASE = process.env.NEXT_PUBLIC_SITE_URL || "https://blowdryonwheels-com.wp-arenastaging.com";

export function waLink(hit: any): string {
  const neg = Array.isArray(hit.crm_negotiator_id) ? hit.crm_negotiator_id[0] || {} : hit.crm_negotiator_id || {};
  const phone = neg.phone || "+971 568 308 221";
  const ref = hit.crm_id || "";
  const type = hit.building?.[0] || hit.building_type || "";
  const price = hit.price ? "AED " + hit.price.toLocaleString("en-US") : "";
  const loc = addressOf(hit);
  const link = propLink(hit);
  const text = `Hello Zoya Ventures,\n\nI would like to know more about this property:\n\n• Reference: ${ref}\n• Type: ${type}\n• Price: ${price}\n• Location: ${loc}\n• Link: ${SITE_BASE}${link}\n\nModifying this message will prevent it from being sent to the agent.`;
  const respPhone = String(neg.phone || "").replace(/\D/g, "");
  const target = respPhone || "971568308221";
  return `https://wa.me/${target}?text=${encodeURIComponent(text)}`;
}
