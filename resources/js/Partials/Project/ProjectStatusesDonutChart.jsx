import {Chart as ChartJS, ArcElement, Tooltip, Legend} from 'chart.js';
import {Doughnut} from "react-chartjs-2"

ChartJS.register(ArcElement, Tooltip, Legend);

export default function ProjectStatusesDonutChart ({project}) {

    let data = {
        labels: [],
        datasets: [
            {
                label: 'Project Statuses',
                data: [],
                backgroundColor: [
                    'rgba(247, 103, 103, 0.8)',
                    'rgba(71, 129, 255, 0.8)',
                    'rgba(95, 226, 112, 0.8)',
                    'rgba(242, 236, 82, 0.8)',
                    'rgba(255, 174, 68, 0.8)',
                    'rgba(82, 244, 249, 0.8)',
                    'rgba(245, 112, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(247, 103, 103, 1)',
                    'rgba(71, 129, 255, 1)',
                    'rgba(95, 226, 112, 1)',
                    'rgba(242, 236, 82, 1)',
                    'rgba(255, 174, 68, 1)',
                    'rgba(82, 244, 249, 1)',
                    'rgba(245, 112, 255, 1)'
                ],
                borderWidth: 2,
            },
        ],
    };

    project.statuses.map((status, index) => {
        data.labels.push(status.name);

        data.datasets[0].data.push(status.tasks_count);
    })

    function totalCount () {
        let count = 0
        for (let i = 0; i < data.datasets[0].data.length; i++) {
            count += data.datasets[0].data[i]
        }
        return count
    }

    return (
        <>
            {totalCount() > 0 && <div>
                <h2 className={'text-center text-2xl'}>{project.name} Statuses</h2>
                <Doughnut data={data}></Doughnut>
            </div>}
        </>
    );
}
