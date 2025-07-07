'use client'

import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { AlertTriangle, ArrowLeft, RefreshCw } from 'lucide-react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'

interface CompanyNotFoundProps {
  companyId?: string
  message?: string
}

export function CompanyNotFound({ 
  companyId, 
  message = 'A rede solicitada não foi encontrada ou não existe mais.' 
}: CompanyNotFoundProps) {
  const router = useRouter()

  const handleRefresh = () => {
    router.refresh()
  }

  return (
    <div className="container mx-auto p-6">
      <div className="max-w-2xl mx-auto">
        <Card className="border-red-200 bg-red-50">
          <CardHeader className="text-center pb-4">
            <div className="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
              <AlertTriangle className="w-8 h-8 text-red-600" />
            </div>
            <CardTitle className="text-xl text-red-800">
              Rede não encontrada
            </CardTitle>
          </CardHeader>
          <CardContent className="text-center space-y-6">
            <div className="space-y-2">
              <p className="text-red-700 font-medium">
                {message}
              </p>
              {companyId && (
                <p className="text-sm text-red-600">
                  ID da rede: <span className="font-mono bg-red-100 px-2 py-1 rounded">#{companyId}</span>
                </p>
              )}
            </div>

            <div className="space-y-3">
              <p className="text-sm text-gray-600">
                Possíveis motivos:
              </p>
              <ul className="text-sm text-gray-600 space-y-1 text-left max-w-md mx-auto">
                <li>• A rede foi removida do sistema</li>
                <li>• O ID fornecido é inválido</li>
                <li>• Você não tem permissão para acessar esta rede</li>
                <li>• Erro temporário de conexão</li>
              </ul>
            </div>

            <div className="flex flex-col sm:flex-row gap-3 justify-center pt-4">
              <Link href="/redes" passHref>
                <Button variant="default" className="w-full sm:w-auto">
                  <ArrowLeft className="w-4 h-4 mr-2" />
                  Voltar para Redes
                </Button>
              </Link>
              <Button 
                variant="outline" 
                onClick={handleRefresh}
                className="w-full sm:w-auto"
              >
                <RefreshCw className="w-4 h-4 mr-2" />
                Tentar Novamente
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
