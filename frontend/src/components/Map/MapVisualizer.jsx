import {EdgeShapes, Sigma} from 'react-sigma';
import {fetchMapData} from '../../lib/api/map';
import {AuthContext} from "../../lib/context/AuthContext";
import {useContext, useEffect, useState} from "react";

function MapVisualizer() {
    const {user} = useContext(AuthContext);
    const [map, setMap] = useState(null);

    const fetchData = async () => {
        const mapData = await fetchMapData(user.token);

        if (mapData) {
            setMap(mapData);
        }
    };

    useEffect(() => {
        fetchData()
    }, []);

    return (
        map &&
        <Sigma
            graph={map}
            settings={{
                drawEdges: true,
                drawEdgeLabels: true,
                clone: false
            }}
        >
            <EdgeShapes default="curvedArrow"/>
        </Sigma>
    );
}

export default MapVisualizer;

