import Link from "next/link";
import Image from "next/image";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

export default function HomePage() {
  return (
    <div className="flex min-h-svh flex-col items-center justify-center bg-muted p-6 md:p-10">
      <div className="w-full max-w-4xl">
        <Card className="overflow-hidden">
          <CardContent className="grid p-0 md:grid-cols-2">
            <div className="p-8 md:p-12 flex flex-col justify-center">
              <div className="space-y-6">
                <div className="text-center md:text-left">
                  <h1 className="text-3xl md:text-4xl font-bold tracking-tight mb-4">
                    Sistema de Gerenciamento de 
                    <span className="text-blue-600"> Mídias Publicitárias</span>
                    <span className="text-blue-600" aria-hidden="true">
                      .
                    </span>
                  </h1>
                  
                  <p className="text-lg text-muted-foreground leading-relaxed">
                    Plataforma completa para gerenciamento de campanhas publicitárias em
                    redes de supermercados. Controle total das suas mídias, monitore 
                    performance e maximize o ROI de suas campanhas em uma única solução 
                    integrada.
                  </p>
                </div>

                <div className="pt-6 space-y-4">
                  <Button size="lg" className="w-full" asChild>
                    <Link
                      href="/login"
                      aria-label="Acessar área de login do sistema"
                    >
                      Acessar Plataforma
                    </Link>
                  </Button>
                  
                  <div className="text-sm text-muted-foreground text-center md:text-left">
                    <p className="flex items-center justify-center md:justify-start gap-2 flex-wrap">
                      <span className="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
                        ✨ Gerencie campanhas
                      </span>
                      <span className="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
                        📊 Relatórios em tempo real
                      </span>
                      <span className="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
                        🎯 Otimize resultados
                      </span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <div className="relative hidden bg-muted md:block">
              <Image
                src="/placeholder.svg"
                alt="Ilustração da plataforma de mídia ads"
                fill
                className="object-cover dark:brightness-[0.2] dark:grayscale"
              />
            </div>
          </CardContent>
        </Card>
        
        <div className="text-balance text-center text-xs text-muted-foreground mt-6">
          &copy; 2025 Plataforma de Mídia Ads. Todos os direitos reservados.
        </div>
      </div>
    </div>
  );
}
