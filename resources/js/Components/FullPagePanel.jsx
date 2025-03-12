export default function FullPagePanel ({title, children}) {

    return (
        <>
            <div className="mx-2 sm:mx-4 md:mx-8 bg-white rounded-md border shadow">
                <div className="px-3 sm:px-4 md:p-8 py-4 border-b border-dashed">
                    {title}
                </div>
                <div className="p-2 sm:p-4 md:p-8 bg-gray-50 rounded-b-md">
                    {children}
                </div>
            </div>
        </>
    );
}
