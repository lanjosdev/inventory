import { CompanyDetails, CompanyNotFound } from '@/components/companies';
import { getCompanyByIdAction } from '@/lib/actions/companyActions';

interface DetailsNetworkPageProps {
    params: {
        id: string;
    };
}

export default async function DetailsNetworkPage({ params }: DetailsNetworkPageProps) {
    const companyId = parseInt(params.id, 10);
    const companyResponse = await getCompanyByIdAction(companyId);

    if (!companyResponse.success || !companyResponse.data) {
        return <CompanyNotFound companyId={params.id} message={companyResponse.message} />;
    }

    const company = companyResponse.data;

    return (
        <div className="container mx-auto p-6">
            <CompanyDetails company={company} />
        </div>
    );
}
