import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SimpleBreadcrumbItem, type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/react';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@radix-ui/react-dropdown-menu';
import { Fragment } from 'react';

export function Breadcrumbs({ breadcrumbs }: { breadcrumbs: BreadcrumbItemType[] }) {
    return (
        <>
            {breadcrumbs.length > 0 && (
                <Breadcrumb>
                    <BreadcrumbList>
                        {breadcrumbs.map((breadItem, index) => {
                            const isLast = index === breadcrumbs.length - 1;

                            // render dropdown breadcrumb item
                            if ('trigger' in breadItem) {
                                return (
                                    <Fragment key={index}>
                                        <BreadcrumbItem>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger>
                                                    {breadItem.trigger}
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent>
                                                    {breadItem.items.map((item: SimpleBreadcrumbItem) => {
                                                        return (
                                                            <DropdownMenuItem className='p-1'>
                                                                <Link href={item.href}>
                                                                    {item.title}
                                                                </Link>
                                                            </DropdownMenuItem>
                                                        )
                                                    })}
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </BreadcrumbItem>
                                        {!isLast && <BreadcrumbSeparator />}
                                    </Fragment>
                                );
                            // render simple breadcrumb item
                            } else {
                                return (
                                    <Fragment key={index}>
                                        <BreadcrumbItem>
                                            {isLast ? (
                                                <BreadcrumbPage>{breadItem.title}</BreadcrumbPage>
                                            ) : (
                                                <BreadcrumbLink asChild>
                                                    <Link href={breadItem.href}>{breadItem.title}</Link>
                                                </BreadcrumbLink>
                                            )}
                                        </BreadcrumbItem>
                                        {!isLast && <BreadcrumbSeparator />}
                                    </Fragment>
                                );
                            }
                        })}
                    </BreadcrumbList>
                </Breadcrumb>
            )}
        </>
    );
}
