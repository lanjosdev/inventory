import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';

export function DashboardSkeleton() {
  return (
    <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
      {/* Stats Cards Skeleton */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {Array.from({ length: 4 }).map((_, i) => (
          <Card key={i}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <Skeleton className="h-4 w-[100px]" />
              <Skeleton className="h-4 w-4" />
            </CardHeader>
            <CardContent>
              <Skeleton className="h-8 w-[80px] mb-2" />
              <Skeleton className="h-3 w-[120px]" />
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Content Grid Skeleton */}
      <div className="grid gap-4 md:grid-cols-2">
        {/* Quick Actions Skeleton */}
        <Card>
          <CardHeader>
            <Skeleton className="h-6 w-[120px]" />
            <Skeleton className="h-4 w-[200px]" />
          </CardHeader>
          <CardContent>
            <div className="grid gap-4 md:grid-cols-2">
              {Array.from({ length: 4 }).map((_, i) => (
                <div key={i} className="h-20 p-4 border rounded-lg">
                  <div className="flex items-center gap-3">
                    <Skeleton className="h-4 w-4" />
                    <div className="flex-1">
                      <Skeleton className="h-4 w-[80px] mb-1" />
                      <Skeleton className="h-3 w-[120px]" />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Recent Activity Skeleton */}
        <Card>
          <CardHeader>
            <Skeleton className="h-6 w-[140px]" />
            <Skeleton className="h-4 w-[240px]" />
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="flex items-center gap-3 p-3 rounded-lg border">
                  <Skeleton className="h-4 w-4" />
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <Skeleton className="h-4 w-[100px]" />
                      <Skeleton className="h-5 w-[50px]" />
                    </div>
                    <Skeleton className="h-3 w-[150px] mb-1" />
                    <Skeleton className="h-3 w-[80px]" />
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
