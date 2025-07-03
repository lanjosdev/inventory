import type { Metadata } from "next";
import localFont from "next/font/local";
import "./globals.css";


const geistSans = localFont({
  src: "./fonts/GeistVF.woff",
  variable: "--font-geist-sans",
  weight: "100 900",
});
const geistMono = localFont({
  src: "./fonts/GeistMonoVF.woff",
  variable: "--font-geist-mono",
  weight: "100 900",
});

export const metadata: Metadata = {
  title: "Plataforma Mídia Ads",
  description: "Plataforma para gerenciamento de redes de supermercados e suas filiais.",
  keywords: "supermercados, filiais, lojas, mídia, publicidade, gerenciamento, CRUD",
  authors: [{ name: "BizSys" }],
  openGraph: {
    title: "Plataforma Mídia Ads",
    description: "Gerencie redes de supermercados e suas filiais de forma eficiente",
    type: "website",
    locale: "pt_BR",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="pt-BR">

      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        {children}
      </body>

    </html>
  );
}
